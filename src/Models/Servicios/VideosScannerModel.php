<?php

class VideoScannerModel
{
private $rutaLinuxBase = '/mnt/videos_alcaldias';
private $rutaUNCBase   = '\\\\172.16.0.55\\alcaldias';

    public function revisarVideos($rutaBase)
    {
        $lista = [];
        $total = 0;
        $videosCortos = 0;

        if (!is_dir($rutaBase)) {
            return [
                'total' => 0,
                'videos_cortos' => 0,
                'lista' => [],
                'error' => 'La ruta no existe o no es accesible'
            ];
        }

        $videos = [];
        $this->buscarVideosRecursivo($rutaBase, $videos);

        foreach ($videos as $rutaVideo) {
            $duracion = $this->obtenerDuracion($rutaVideo);
            $total++;

            // 🔁 convertir ruta Linux → UNC
            $rutaUNC = str_replace(
                $this->rutaLinuxBase,
                $this->rutaUNCBase,
                $rutaVideo
            );

            $rutaCarpetaUNC = dirname($rutaUNC);

            // normalizar slashes para Windows
            $rutaUNC = str_replace('/', '\\', $rutaUNC);

            if ($duracion !== null) {
                $lista[] = [
                    'ruta_linux'   => $rutaVideo,
                    'ruta_unc'     => $rutaUNC,                 // archivo
                    'ruta_carpeta' => dirname($rutaUNC),         // 📂 carpeta contenedora
                    'archivo'      => basename($rutaVideo),
                    'duracion'     => round($duracion, 2),
                    'es_corto'     => $duracion < 10
                ];

                if ($duracion < 10) {
                    $videosCortos++;
                }
            }
        }

        return [
            'total' => $total,
            'videos_cortos' => $videosCortos,
            'lista' => $lista
        ];
    }

    private function buscarVideosRecursivo($ruta, &$lista)
    {
        foreach (scandir($ruta) as $archivo) {
            if ($archivo === '.' || $archivo === '..') continue;

            $rutaCompleta = $ruta . '/' . $archivo;

            if (is_dir($rutaCompleta)) {
                $this->buscarVideosRecursivo($rutaCompleta, $lista);
            } elseif (preg_match('/\.(mp4|avi|mov|mts)$/i', $archivo)) {
                $lista[] = $rutaCompleta;
            }
        }
    }

    // private function obtenerDuracion($rutaVideo)
    // {
    //     $cmd = "ffprobe -v error -show_entries format=duration "
    //          . "-of default=noprint_wrappers=1:nokey=1 "
    //          . escapeshellarg($rutaVideo);

    //     $output = shell_exec($cmd);

    //     return $output ? (float)$output : null;
    // }
    
    private function obtenerDuracion($rutaVideo)
    {
        $cmd = "ffprobe -v error "
            . "-show_entries format=duration "
            . "-of default=noprint_wrappers=1:nokey=1 "
            . "-read_intervals %+10 "
            . escapeshellarg($rutaVideo);

        $output = shell_exec($cmd);
        return $output ? (float)$output : null;
    }

}

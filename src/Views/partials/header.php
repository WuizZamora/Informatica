<header class="p-3 bg-success text-white">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <a href="/INFORMATICA/index.php" class="mb-2 mb-lg-0 text-white text-decoration-none">
        <img src="./public/images/logo_cdmx.png" alt="LOGO CDMX" width="30"> INVEA
      </a>

      <p class="m-0">Bienvenido <span class="text-uppercase"><?php echo $mensaje; ?></span></p>

      <div class="d-flex align-items-center">
        <form action="./src/auth/logout.php" method="post">
            <!-- <button type="submit" class="btn btn-primary">Cerrar Sesión</button> -->
            <button type="submit" class="btn btn-outline-light me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"></path>
                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"></path>
                </svg>
                Salir
            </button>
        </form>
      </div>
    </div>
</header>    
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <a href="/INFORMATICA/index.php" class="mb-2 text-white text-decoration-none d-flex align-items-center me-3">
      <img src="./public/images/cdmx_logo_completo.png" alt="LOGO CDMX" width="80" class="me-2"> INVEA
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="d-flex justify-content-between align-items-center w-100">
        <ul class="navbar-nav d-flex align-items-center me-auto">
          <li class="nav-item mx-5">
            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'servicios') ? 'active' : ''; ?>" href="?page=servicios">Servicios</a>
          </li>
          <li class="nav-item mx-5">
            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'activos') ? 'active' : ''; ?>" href="?page=activos">Activos</a>
          </li>
          <li class="nav-item mx-5">
            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'personal') ? 'active' : ''; ?>" href="?page=personal">Personal</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Hola, <?php echo $nombrePersonal; ?>
            </a>
            <ul class="dropdown-menu">
              <li>
                <form action="./src/auth/logout.php" method="post" class="d-flex">
                  <button type="submit" class="dropdown-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"></path>
                      <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"></path>
                    </svg>
                    Salir
                  </button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
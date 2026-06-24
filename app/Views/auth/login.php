<?= $this->extend('template/auth_header') ?>

<?= $this->section('content') ?>

<section class="p-3 p-md-4 p-xl-5 vh-100 d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xxl-11">
        <div class="card border-light-subtle shadow-sm">
          <div class="row g-0">

            <div class="col-12 col-md-6">
              <img class="img-fluid rounded-start w-100 h-100 object-fit-cover"
                src="<?= base_url('images/edificio.jpg') ?>"
                alt="Condominio Residencial"
                style="min-height: 520px; object-position: center;"
                loading="lazy">
            </div>

            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
              <div class="col-12 col-lg-11 col-xl-10">
                <div class="card-body p-3 p-md-4 p-xl-5">

                  <div class="row">
                    <div class="col-12">
                      <div class="mb-4 text-center">
                        <a href="<?= base_url('/') ?>">
                          <img src="<?= base_url('images/agora.jpg') ?>" alt="Agora Logo" style="max-width: 200px; width: 100%; height: auto; border-radius: 8px;">
                        </a>
                        <h2 class="h5 text-center fw-bold text-dark mt-3">Sistema de Gestión de Condominios</h2>
                        <p class="text-center text-muted small">Ingresa tus credenciales para acceder</p>
                      </div>
                    </div>
                  </div>
                  <!-- aqui se encuentra la conexion con el backend :) -->
                  <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger border-0 rounded-3 mb-3 small" role="alert">
                      <i class="bi bi-exclamation-circle-fill me-2"></i>
                      <?= session()->getFlashdata('error') ?>
                    </div>
                  <?php endif; ?>

                  <form action="<?= site_url('auth/procesar-login') ?>" method="POST" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <div class="row gy-3 overflow-hidden">


                      <div class="col-12">
                        <div class="form-floating mb-1">
                          <input type="email" class="form-control" name="correo" id="correo" placeholder="nombre@correo.com" required>
                          <label for="correo" class="form-label text-secondary">Correo Electrónico</label>
                          <div class="invalid-feedback small ps-2">Por favor, ingresa tu correo electrónico corporativo o residencial.</div>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-floating mb-1">
                          <input type="password" class="form-control" name="clave" id="clave" placeholder="Contraseña" required>
                          <label for="clave" class="form-label text-secondary">Contraseña</label>
                          <div class="invalid-feedback small ps-2">Por favor, escribe la contraseña asociada a tu cuenta.</div>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="recuerdame" id="recuerdame">
                          <label class="form-check-label text-secondary small" for="recuerdame">
                            Mantener sesión iniciada
                          </label>
                        </div>
                      </div>

                      <div class="col-12">
                        <div class="d-grid">
                          <button class="btn btn-agora btn-lg fw-bold" type="submit">Ingresar</button>
                        </div>
                      </div>
                    </div>
                  </form>

                  <div class="col-12 text-center mt-3">
                    <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted small fw-semibold d-inline-flex align-items-center justify-content-center gap-1">
                      ← Volver al inicio
                    </a>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <p class="mb-0 mt-5 text-secondary text-center small">
                        ¿Olvidaste tus datos? Llama a XXXX-XXXX 
                      </p>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>

<?= $this->include('template/auth_footer') ?>
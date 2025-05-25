<?php
namespace Entity;

class Usuario
   {
      private ?int $id;
      private ?string $nombre;
      private ?string $apellido;
      private ?\DateTimeInterface $fechaNacimiento;
      private ?string $sexo;
      private string $correo;
      private string $contraseniaHash;
      private string $nombreUsuario;
      private ?string $urlFotoPerfil;
      private string $urlQr;
      private int $idRol;
      private ?int $idCiudad;
      private int $idNivel = 1;
      private int $puntajeTotal = 0;
      private bool $cuentaValidada = false;

      public function __construct(array $data = [])
      {
         $this->id = $data['id'] ?? null;
         $this->nombre = $data['nombre'] ?? null;
         $this->apellido = $data['apellido'] ?? null;

         $this->fechaNacimiento = isset($data['fecha_nacimiento'])
            ? new \DateTime($data['fecha_nacimiento']) : null;
         $this->sexo = $data['genero'] ?? null;
         $this->correo = $data['correo'];
         $this->contraseniaHash = $data['contrasenia'];
         $this->nombreUsuario = $data['nombre_usuario'];
         $this->urlFotoPerfil = $data['url_foto_perfil'] ?? null;
         $this->urlQr = $data['url_qr'] ?? $this->generarUrlQr();
         $this->idRol = $data['id_rol'] ?? 3;
         $this->idCiudad = $data['id_ciudad'] ?? null;
         $this->idNivel = $data['id_nivel'] ?? 1;
         $this->puntajeTotal = $data['puntaje_total'] ?? 0;
         $this->cuentaValidada = (bool)($data['cuenta_validada'] ?? false);
      }


      //solucion paleativa.. investigar generar QR jeje
      private function generarUrlQr(): string
      {
         return "/qr/{$this->nombreUsuario}.png";
      }

      public function hashContrasenia(string $contraseniaPlana): void
      {
         $this->contraseniaHash = password_hash($contraseniaPlana, PASSWORD_DEFAULT);
      }

      // Verifica contraseña (para login)
      public function verificarContrasenia(string $contraseniaPlana): bool
      {
         return password_verify($contraseniaPlana, $this->contraseniaHash);
      }

      public function getRol(): int
      {
         return $this->idRol;
      }

      public function esAdmin(): bool
      {
         return $this->idRol === 1;
      }

      public function esModerador(): bool
      {
         return $this->idRol === 2;


      }

      public function esUsuarioComun(): bool
      {
         return $this->idRol === 3;
      }

      public function getId(): ?int
      {
         return $this->id;
      }

      public function getNombre(): ?string
      {
         return $this->nombre;
      }

      public function getApellido(): ?string
      {
         return $this->apellido;
      }

      public function getFechaNacimiento(): ?\DateTimeInterface
      {
         return $this->fechaNacimiento;
      }

      public function getSexo(): ?string
      {
         return $this->sexo;
      }

      public function getCorreo(): string
      {
         return $this->correo;
      }

      public function getContraseniaHash(): string
      {
         return $this->contraseniaHash;
      }

      public function getNombreUsuario(): string
      {
         return $this->nombreUsuario;
      }

      public function getUrlFotoPerfil(): ?string
      {
         return $this->urlFotoPerfil;
      }

      public function getUrlQr(): string
      {
         return $this->urlQr;
      }

      public function getIdRol(): int
      {
         return $this->idRol;
      }

      public function getIdCiudad(): ?int
      {
         return $this->idCiudad;
      }

      public function getIdNivel(): int
      {
         return $this->idNivel;
      }


      public function getPuntajeTotal(): int
      {
         return $this->puntajeTotal;
      }

      public function getCuentaValidada(): bool
      {
         return $this->cuentaValidada;
      }

      public function setId(?int $id): void
      {
         $this->id = $id;
      }

      public function setUrlFotoPerfil(?string $urlFotoPerfil): void
      {
         $this->urlFotoPerfil = $urlFotoPerfil;
      }
   }

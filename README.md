# sigeru
SiGeRU - Urban Waste Management System
# SiGeRU - Sistema de Gestión de Residuos Urbanos

## Equipo
Whale Solutions

## Integrantes
- Ignacio Menéndez
- Mathías Silveira
- Rodrigo Morelli

## Descripción

SiGeRU es un sistema web desarrollado como proyecto académico que permite gestionar diferentes procesos relacionados con la recolección de residuos.

El sistema cuenta con un frontend para la interacción con los usuarios y varias APIs desarrolladas en PHP encargadas de la gestión de usuarios, incidencias, recolección y contenedores.

---

# Requisitos

Antes de ejecutar el proyecto es necesario contar con:

- XAMPP instalado.
- Apache iniciado.
- MySQL iniciado.
- PHP 8 o superior (incluido en XAMPP).
- Navegador web.

---

# Cómo levantar el proyecto

## 1. Copiar el proyecto

Copiar la carpeta completa del proyecto dentro de la carpeta:

```text
C:\xampp\htdocs\
```

Debe quedar una estructura similar a:

```text
htdocs/
└── sigeru/
    ├── frontend/
    ├── api-usuarios/
    ├── api-incidencias/
    ├── api-recoleccion/
    ├── api-gestion/
```

---

## 2. Iniciar XAMPP

Abrir el Panel de Control de XAMPP e iniciar:

- Apache
- MySQL

---

## 3. Abrir el sistema

El archivo index se encuentra dentro de la carpeta **frontend**.

Ingresar desde el navegador a:

```text
http://localhost/sigeru/frontend/
```

o abrir directamente el archivo correspondiente:

```text
http://localhost/sigeru/frontend/index.html
```

---

## 4. APIs

El proyecto utiliza distintas APIs desarrolladas en PHP para separar las responsabilidades del sistema.

Estas APIs son:

- api-usuarios
- api-recoleccion
- api-gestion

Cada una procesa las solicitudes correspondientes al módulo que administra.

---

# Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- Bootstrap
- PHP
- MySQL
- XAMPP

---

# Estructura del proyecto

```text
sigeru/
│
├── frontend/
│
├── api-usuarios/
│
├── api-recoleccion/
│
├── api-gestion/
│
└── README.md
```

---

# Observaciones

Para que el sistema funcione correctamente es necesario que todas las carpetas del proyecto permanezcan dentro de `htdocs`, ya que el frontend consume las APIs mediante rutas locales.
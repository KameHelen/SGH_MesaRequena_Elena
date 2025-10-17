# Sistema de Gestión Hotelera (SGH)

**Nombre del proyecto:** SGH_MesaRequena_Elena 
**Curso:** Segundo de aplicaciones multiplataforma  
**Autor:** Elena Mesa Requena  
**Fecha:** Octubre 2025

---

## 📌 Descripción

El **Sistema de Gestión Hotelera (SGH)** es una aplicación web desarrollada en **PHP con MySQL** que permite al Hotel "El Gran Descanso" gestionar de forma eficiente:

- Registro de huéspedes
- Gestión de habitaciones (número, tipo, precio, estado de limpieza)
- Creación y control de reservas
- Programación de tareas de mantenimiento

La aplicación cumple con las siguientes **reglas de negocio críticas**:

1. ❌ Una habitación **no puede reservarse** si ya tiene una **reserva confirmada** en fechas solapadas.
2. ❌ Una habitación **no puede asignarse** si tiene una **tarea de mantenimiento activa** que coincida con las fechas de la reserva.

---

## 🗂️ Estructura del Proyecto

SGH_ApellidosNombre/
├── config.php
├── public/
│   └── index.php
├── modelo/
│   ├── Habitacion.php
│   ├── Huesped.php
│   ├── Reserva.php
│   └── TareaMantenimiento.php
├── controlador/
│   ├── HabitacionController.php
│   ├── HuespedController.php
│   ├── ReservaController.php
│   └── MantenimientoController.php
└── vista/
    ├── admin/
    │   ├── habitaciones.php
    │   ├── huespedes.php
    │   ├── reservas.php
    │   └── mantenimiento.php
    └── publica/
        └── reservar.php

> 🔒 Solo los archivos dentro de `public/` deben ser accesibles directamente desde el navegador.  
> Las carpetas `vista_admin/` y `vista_publica/` **se incluyen desde PHP**, no se acceden directamente.

---

## ⚙️ Requisitos

- Servidor web con **PHP 8.0 o superior**
- **MySQL 5.7+** o **MariaDB**
- Extensiones PHP: `pdo`, `pdo_mysql`

---

## 🚀 Instalación

1. **Clona o descarga** este repositorio.
2. **Crea una base de datos** en MySQL llamada `sgh`:
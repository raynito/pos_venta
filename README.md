# Sistema de Punto de Venta (USD ⇄ BsD)

Un sistema completo de punto de venta con conversión automática de moneda entre Dólares Americanos (USD) y Bolívares Digitales (BsD).

## 📋 Módulos del Sistema

### 1. 🛒 **Compras**
- **1.1. Nueva Compra** - Registro de compras a proveedores
- **1.2. Historial de Compras** - Consulta y gestión de compras realizadas

### 2. 💰 **Ventas**
- **2.1. Nueva Venta** - Proceso completo de venta al cliente
- **2.2. Historial de Ventas** - Registro y seguimiento de ventas

### 3. 👥 **Clientes**
- Creación de nuevos clientes
- Modificación de datos de clientes
- Activación/Desactivación de clientes

### 4. 📂 **Categorías**
- Creación de categorías de productos
- Modificación de categorías
- Activación/Desactivación de categorías

### 5. 📏 **Medidas**
- Creación de unidades de medida
- Modificación de medidas
- Activación/Desactivación de medidas

### 6. 🏷️ **Marcas**
- Creación y configuración de marcas
- Modificación de información de marcas
- Activación/Desactivación de marcas
- Asociación de marcas a productos

### 7. 📦 **Productos**
- Creación de productos con información completa
- Modificación de productos existentes
- Activación/Desactivación de productos
- **Carga de imágenes** para productos

### 8. ⚙️ **Administración**
- **8.1. Usuarios**
  - Creación de usuarios del sistema
  - Modificación de datos de usuario
  - Activación/Desactivación de usuarios
  - Gestión de permisos y roles
  
- **8.2. Configuración de la Empresa**
  - Configuración general del negocio
  - **Tasa del día configurable**
  - **Obtener tasa automática por API** (funcionalidad incorporada)

### 9. 🏦 **Cajas**
- **9.1. Cajas**
  - Creación de cajas registradoras
  - Modificación de cajas
  - Activación/Desactivación de cajas
  
- **9.2. Cierres de Caja**
  - Apertura de caja con monto inicial
  - Cierre de caja con arqueo automático
  - Reportes de cierre diario

## 🔧 Características Principales

- **Conversión automática USD ⇄ BsD**
- **API de tasas** para conversión automática
- **Gestión de inventario** en tiempo real
- **Reportes y estadísticas**
- **Interfaz intuitiva** y responsiva
- **Backup y recuperación** de datos

## 💻 Tecnologías Utilizadas

- **Backend:** PHP, MySQL
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap
- **Librerías:** jQuery, DataTables, SweetAlert2
- **APIs:** Conversión de tasas de cambio

## 📊 Funcionalidades Adicionales

- **Control de stock** con alertas de inventario bajo
- **Ventas por cliente** con historial detallado
- **Arqueo de caja** con cálculos automáticos
- **Permisos por usuario** para control de acceso
- **Exportación de datos** a Excel/PDF

## 🚀 Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/raynito/pos_venta.git
# 🛠️ Guía de Configuración: Servidor Local con Dominio Propio

Esta guía te ayudará a configurar tu PC como un servidor permanente para **CERRAJERÍA SARRIA** usando el dominio `stokmaster.com.co` a través de Cloudflare Tunnels.

---

## 1. Requisitos Previos en la PC Servidor
*   **Apache/PHP Corriendo:** XAMPP, Laragon o similar debe estar activo.
*   **Acceso Local:** Debes poder entrar a `http://localhost` y ver el sistema.
*   **Cuenta de Cloudflare:** El dominio `stokmaster.com.co` debe estar gestionado en Cloudflare.

---

## 2. Instalación de Cloudflared
Abre **PowerShell** como Administrador y ejecuta:
```powershell
winget install Cloudflare.cloudflared
```
*Si falla, descarga el instalador `.msi` desde: https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.msi*

---

## 3. Configuración del Túnel (Paso a Paso)

1.  **Iniciar Sesión:**
    ```powershell
    cloudflared tunnel login
    ```
    *Se abrirá el navegador. Inicia sesión y selecciona `stokmaster.com.co`.*

2.  **Crear el Túnel:**
    ```powershell
    cloudflared tunnel create cerrajeria-server
    ```
    *⚠️ IMPORTANTE: Copia el ID (ej: `12345678-abcd-1234-abcd-1234567890ab`) que aparecerá en pantalla.*

3.  **Vincular el Dominio:**
    ```powershell
    cloudflared tunnel route dns cerrajeria-server stokmaster.com.co
    ```

---

## 4. Configuración del Servicio (Inicio Automático)

Para que el túnel funcione siempre, incluso después de reiniciar la PC:

1.  Crea la carpeta `C:\Cloudflare`.
2.  Crea un archivo llamado `config.yml` dentro de esa carpeta con el siguiente contenido (reemplaza los `<ID>`):

```yaml
tunnel: <PEGA-AQUI-EL-ID-DE-TU-TUNNEL>
credentials-file: C:\Users\<TU-USUARIO>\.cloudflared\<PEGA-AQUI-EL-ID-DE-TU-TUNNEL>.json

ingress:
  - hostname: stokmaster.com.co
    service: http://localhost:80
  - service: http_status:404
```

3.  **Instalar el servicio en Windows:**
    Regresa a PowerShell (como Administrador) y ejecuta:
    ```powershell
    cloudflared service install C:\Cloudflare\config.yml
    ```
4.  **Iniciar el servicio:**
    ```powershell
    Start-Service CloudflareDeamon
    ```

---

## 5. Ajuste Final en CakePHP
En la PC servidor, dentro de la carpeta del proyecto, edita el archivo `config/.env` o `config/app_local.php`:

*   **URL Base:** Asegúrate de que apunte a tu dominio:
    `export APP_FULL_BASE_URL="https://stokmaster.com.co"`
*   **Base de Datos:** Verifica que el `DATABASE_HOST` sea `localhost` o `127.0.0.1`.

---

## ✅ ¡Listo!
Ahora puedes acceder a tu sistema desde cualquier lugar del mundo entrando a:
👉 **https://stokmaster.com.co**

---
*Nota: Asegúrate de que el Firewall de Windows permita el tráfico en el puerto 80 si tienes problemas de conexión.*

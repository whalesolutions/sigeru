const formularioLogin = document.getElementById("loginForm");
const mensaje = document.getElementById("mensajeLogin");

formularioLogin.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const datosLogin = {
        correo: formularioLogin.email.value,
        contrasena: formularioLogin.password.value
    };

    try {

        const respuesta = await fetch(
            "http://localhost:8080/sigeru/api-usuarios/index.php/usuarios/login",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datosLogin)
            }
        );

        const resultado = await respuesta.json();
        console.log(resultado);

        if (respuesta.ok) {
            
            localStorage.setItem("usuario", JSON.stringify(resultado.usuario));
            mensaje.textContent = resultado.mensaje;
            mensaje.style.color = "green";
            setTimeout(function () {
                 console.log("Voy al dashboard");
                window.location.href = "dashboard.html";
            }, 1000);

        } else {
            mensaje.textContent = resultado.mensaje;
            mensaje.style.color = "red";
        }

    } catch (error) {
        console.error(error);
        mensaje.textContent = "Error al conectar con el servidor.";
        mensaje.style.color = "red";
    }
});
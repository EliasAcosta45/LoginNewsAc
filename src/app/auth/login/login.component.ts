import { Component,OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { LoginService } from 'src/app/auth/login-service.service'; // Asegúrate de que la ruta sea correcta
import { jwtDecode } from 'jwt-decode';
import { window as rxjsWindow } from 'rxjs';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  showPassword: boolean = false; // Nueva propiedad para controlar la visibilidad de la contraseña

  constructor(private loginService: LoginService, private router: Router) {}

  ngOnInit(): void {
    const token = localStorage.getItem('Token');
    if (token) {
      try {
        const decodedToken: any = jwtDecode(token);
        const role = decodedToken.data.role;
        this.redirectBasedOnRole(role); 
      } catch (error) {
        console.error('Error al decodificar el token:', error);
      }
    }
  }

  // Función para iniciar sesión
  login() {
    const loginData = { username: this.email, password: this.password };

    this.loginService.login(loginData).subscribe({
      next: (response) => {
        if (response.Token) {
          localStorage.setItem('Token', response.Token);
          try {
            const decodedToken: any = jwtDecode(response.Token);
            const role = decodedToken.data.role;
            this.redirectBasedOnRole(role);
          } catch (error) {
            console.error('Error al decodificar el token:', error);
          }
        }
      },
      error: (error) => {
        console.error('Error en la API:', error);
      }
    });
  }

  // Función para redirigir según el rol
  redirectBasedOnRole(role: number) {
    if (role) {
      this.router.navigate(['/home']);
    }
  }

  // Función para alternar la visibilidad de la contraseña
  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  register() {
    this.router.navigate(['/register']);
  }
}

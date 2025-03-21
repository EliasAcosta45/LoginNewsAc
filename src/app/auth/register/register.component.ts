import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { RegisterService } from 'src/app/auth/register.service'; // Importamos el servicio RegisterService

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  registerData = {
    username: '',
    password: '',
    confirmPassword: ''
  };

  showPassword: boolean = false; // Nueva propiedad para controlar la visibilidad de la contraseña
  showConfirmPassword: boolean = false; // Nueva propiedad para controlar la visibilidad de la confirmación de contraseña

  constructor(private registerService: RegisterService, private router: Router) {}

  onSubmit() {
    if (this.registerData.password !== this.registerData.confirmPassword) {
      alert('Las contraseñas no coinciden');
      return;
    }

    this.registerService.register(this.registerData).subscribe({
      next: (response) => {
        alert(response.message);
        this.router.navigate(['/auth/login']);
      },
      error: (err) => {
        console.error('Error en el registro:', err);
        alert('Error al registrar el usuario');
      }
    });
  }

  // Funciones para alternar la visibilidad de las contraseñas
  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  toggleConfirmPasswordVisibility() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }
}

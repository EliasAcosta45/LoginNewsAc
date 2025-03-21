// src/app/auth/recover.component.ts
import { Component } from '@angular/core';
import { RecoverService } from '../recover-service.service';

@Component({
  selector: 'app-recover-password',
  templateUrl: './recover.component.html',
  styleUrls: ['./recover.component.css']
})
export class RecoverPasswordComponent {
  email: string = '';
  code: string = ''; // Este campo puede ser el token en lugar del código
  newPassword: string = '';
  isCodeSent: boolean = false;

  constructor(private recoverService: RecoverService) {}

  // Enviar el código al correo
  sendRecoveryCode() {
    this.recoverService.sendRecoveryCode(this.email).subscribe(
      response => {
        if (response && response.success) {
          alert('Código de recuperación enviado a tu correo.');
          this.isCodeSent = true;
        } else {
          alert('No se pudo enviar el código. Por favor, verifica el correo ingresado.');
        }
      },
      error => {
        alert('Error al enviar el código. Intenta nuevamente.');
      }
    );
  }

    // Verificar el código y restablecer la contraseña
    resetPassword() {
      this.recoverService.resetPassword(this.newPassword, this.code).subscribe(
        response => {
          if (response && response.message) {
            alert('Contraseña cambiada con éxito.');
          } else {
            alert('Código o nueva contraseña inválidos.');
          }
        },
        error => {
          alert('Error al restablecer la contraseña. Intenta nuevamente.');
        }
      );
    }
  }
  

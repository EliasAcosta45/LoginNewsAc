// src/app/auth/recover.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RecoverService {

  private apiUrl = 'http://localhost/APIEliasAcosta'; // Asegúrate de que esta URL sea correcta

  constructor(private http: HttpClient) {}

  // Enviar código de recuperación al correo
  sendRecoveryCode(email: string): Observable<any> {
    const body = { email };
    return this.http.post<any>(`${this.apiUrl}/recover.php`, body);
  }

  // Restablecer la contraseña
  resetPassword(newPassword: string, token: string): Observable<any> {
    const body = { newPassword };
    return this.http.post<any>(`${this.apiUrl}/reset-password.php?token=${token}`, body); // Asegúrate de que el endpoint sea correcto
  }
}

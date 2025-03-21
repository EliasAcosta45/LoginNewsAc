import { Injectable } from '@angular/core';
import { jwtDecode } from 'jwt-decode'; // Asegúrate de instalar 'jwt-decode'

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor() {}

  // Verifica si el token de autenticación está presente y es válido
  isAuthenticated(): boolean {
    const token = localStorage.getItem('Token');
    if (token) {
      const decodedToken: any = jwtDecode(token);
      const expirationDate = new Date(decodedToken.exp * 1000); // JWT expira en segundos, lo convertimos a milisegundos
      if (new Date() < expirationDate) {
        return true;
      } else {
        console.log('El token ha expirado');
      }
    } else {
      console.log('No se encontró token');
    }
    return false;
  }
}

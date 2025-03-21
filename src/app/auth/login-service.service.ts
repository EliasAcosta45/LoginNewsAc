import { HttpClient, HttpErrorResponse, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { jwtDecode } from 'jwt-decode';

@Injectable({
  providedIn: 'root'
})
export class LoginService {

  private apiUrl = 'http://localhost/APIEliasAcosta'; // Asegúrate de que la ruta sea correcta
  private statusCode: any;

  constructor(private http: HttpClient) { }

  // Login con datos de usuario y contraseña
  login(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login.php`, data).pipe(
      catchError((error: HttpErrorResponse) => {
        return throwError(() => new Error(error.error.message || 'Ocurrió un error en el servidor.'));
      })
    );
  }

  // Registro de nuevo usuario
  register(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/register.php`, data).pipe(
      catchError(error => {
        return throwError(() => new Error(error.error.message || 'Ocurrió un error en el servidor.'));
      })
    );
  }

  // Método para comprobar si el usuario está autenticado
  isAuthenticate(): boolean {
    const token: any = localStorage.getItem('Token');

    if (token == null) {
      return false;
    } else {
      const decoded: any = jwtDecode(token);
      const fechaActual = Date.now() / 1000; // Convertir a segundos
      // Verificar si el token ha expirado
      return decoded.exp > fechaActual;
    }
  }
}

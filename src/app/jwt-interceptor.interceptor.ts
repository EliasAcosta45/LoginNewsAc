import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class JwtInterceptorInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    const token = localStorage.getItem('Token'); // Obtener el token desde el almacenamiento local
    if (token) {
      const clonedRequest = request.clone({
        setHeaders: {
          Authorization: `Bearer ${token}` // Agregar el token en el encabezado
        }
      });
      return next.handle(clonedRequest); // Pasar la solicitud modificada
    }

    // Si no hay token, continuar con la solicitud original
    return next.handle(request);
  }
}

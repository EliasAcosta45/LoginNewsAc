import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(): boolean {
    const isAuthenticated = this.authService.isAuthenticated();
    console.log('AuthGuard ejecutado, autenticado:', isAuthenticated); // Log para depuración

    if (isAuthenticated) {
      return true;
    } else {
      console.log('Redirigiendo al login...');
      this.router.navigate(['/login']);
      return false;
    }
  }
}

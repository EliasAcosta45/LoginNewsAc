import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  isLoggedIn: boolean = false;
  currentYear: number = new Date().getFullYear();
  constructor(private Router: Router) {}

  ngOnInit(): void {
    // Verificar si existe un token en el localStorage
    const token = localStorage.getItem('Token');
    this.isLoggedIn = token ? true : false;
  }
  logout() {
    localStorage.removeItem('Token'); // Eliminar el token del localStorage
    this.isLoggedIn = false; // Cambiar el estado a no autenticado
    this.Router.navigate(['/login']);
}
}

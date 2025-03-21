import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class UserdbServiceService {
  private apiUrl = 'http://localhost/APIEliasAcosta/user.php'; // URL de tu API

  constructor(private http: HttpClient) {}

  // Método para obtener todos los usuarios
  getUsers(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  // Método para buscar un usuario por nombre
  searchUser(userName: string): Observable<any> {
    const url = `${this.apiUrl}?UserName=${encodeURIComponent(userName)}`;
    return this.http.get(url);
  }

  // Método para actualizar un usuario
  updateUser(idUser: number, userName: string, role: string): Observable<any> {
    const body = { IdUser: idUser, UserName: userName, Role: role };
    return this.http.put(this.apiUrl, body);
  }

  // Método para eliminar un usuario
  deleteUser(idUser: number): Observable<any> {
    const url = `${this.apiUrl}?idUser=${idUser}`;
    return this.http.delete(url);
  }
}

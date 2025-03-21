import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class UserFavoritesService {
  private apiUrl = 'http://localhost/APIEliasAcosta/user_favorites.php';

  constructor(private http: HttpClient) {}

  // Método para obtener todos los favoritos
  getFavorites(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  // Método para buscar favoritos por nombre
  searchFavorites(favNombre: string): Observable<any> {
    const url = `${this.apiUrl}?favNombre=${encodeURIComponent(favNombre)}`;
    return this.http.get(url);
  }

  // Método para obtener un favorito específico por ID (si es necesario)
  getFavoriteById(idFavorite: number): Observable<any> {
    const url = `${this.apiUrl}?idFavorite=${idFavorite}`;
    return this.http.get(url);
  }

  // Método para crear un nuevo favorito
  createFavorite(favNombre: string, NewsletterId: number, UserId: number): Observable<any> {
    const body = { favNombre, NewsletterId, UserId };
    return this.http.post(this.apiUrl, body);
  }

  // Método para actualizar un favorito
  updateFavorite(idFavorite: number, favNombre: string): Observable<any> {
    const body = { idFavorite, favNombre };
    return this.http.put(this.apiUrl, body);
  }

  // Método para eliminar un favorito
  deleteFavorite(idFavorite: number): Observable<any> {
    const url = `${this.apiUrl}?idFavorite=${idFavorite}`;
    return this.http.delete(url);
  }
}

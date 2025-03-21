import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/shared/services/api.service';
import { UserFavoritesService } from 'src/app/services/user-favorites.service';
import { Newsletter } from '../../models/newsletter.model';

@Component({
  selector: 'app-common-table',
  templateUrl: './common-table.component.html',
  styleUrls: ['./common-table.component.css']
})
export class CommonTableComponent implements OnInit {
  newsletters: Newsletter[] = [];
  role: number = 0; // 2 = Admin, 1 = Usuario
  favorites: number[] = []; // Array de IDs de noticias favoritas
  userId: number = 0; // Almacena el ID del usuario

  constructor(private router: Router, private apiService: ApiService, private userFavoritesService: UserFavoritesService) {}

  ngOnInit(): void {
    if (!localStorage.getItem('hasReloaded')) {
      localStorage.setItem('hasReloaded', 'true');
      window.location.reload();
    }

    const token = localStorage.getItem('Token');
    if (token) {
      try {
        const decodedToken: any = JSON.parse(atob(token.split('.')[1]));
        this.role = decodedToken.data.role; // Obtener el rol del usuario
        this.userId = decodedToken.data.id; // Obtener el ID del usuario
      } catch (error) {
        console.error('Error al decodificar el token:', error);
      }
    }

    // Obtener datos de las newsletters
    this.apiService.getNewsletters().subscribe({
      next: (data) => {
        this.newsletters = data;
      },
      error: (err) => {
        console.error("Error al obtener newsletters:", err);
      }
    });
  }

  toggleFavorite(news: Newsletter) {
    const index = this.favorites.indexOf(news.idNew);
    if (index === -1) {
      // Si no está en favoritos, agregarlo
      this.favorites.push(news.idNew);
      // Crear un nuevo favorito en la base de datos
      this.userFavoritesService.createFavorite(news.Newtitle, news.idNew,this.userId ).subscribe(
        (response) => {
          console.log('Favorito creado:', response);
        },
        (error) => {
          console.error('Error al crear favorito:', error);
        }
      );
    } else {
      // Si ya está en favoritos, eliminarlo
      this.favorites.splice(index, 1);
      // Eliminar el favorito de la base de datos
      this.userFavoritesService.deleteFavorite(news.idNew).subscribe(
        (response) => {
          console.log('Favorito eliminado:', response);
        },
        (error) => {
          console.error('Error al eliminar favorito:', error);
        }
      );
    }
  }

  isFavorite(news: Newsletter): boolean {
    return this.favorites.includes(news.idNew);
  }

  goToUsers() {
    this.router.navigate(['/admin/users']);
  }

  goToFavorites() {
    this.router.navigate(['/favorites']);
  }
}

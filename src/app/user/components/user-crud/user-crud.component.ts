import { Component, OnInit } from '@angular/core';
import { UserFavoritesService } from 'src/app/services/user-favorites.service';

interface Favorite {
  idFavorite: number; // Este campo no se mostrará en la vista
  Fecha: string; // Cambia el tipo según lo que devuelva tu API
  favNombre: string;
  Newtitle: string; // Nuevo campo
}

@Component({
  selector: 'app-user-crud',
  templateUrl: './user-crud.component.html',
  styleUrls: ['./user-crud.component.css']
})
export class UserCrudComponent implements OnInit {
  favorites: Favorite[] = [];
  filteredFavorites: Favorite[] = [];
  searchQuery: string = '';
  selectedFavorite: Favorite | null = null;
  updatedName: string = '';

  constructor(private favoritesService: UserFavoritesService) {}

  ngOnInit(): void {
    this.loadFavorites();
  }

  loadFavorites(): void {
    this.favoritesService.getFavorites().subscribe(
      (data: Favorite[]) => {
        this.favorites = data;
        this.filteredFavorites = data; // Inicialmente, la lista filtrada es igual a la original
      },
      (error) => {
        console.error('Error fetching favorites:', error);
      }
    );
  }

  searchFavorites(): void {
    if (this.searchQuery.trim()) {
      this.favoritesService.searchFavorites(this.searchQuery).subscribe(
        (data: Favorite[]) => {
          this.filteredFavorites = data;
        },
        (error) => {
          console.error('Error searching favorites:', error);
        }
      );
    } else {
      this.filteredFavorites = this.favorites; // Si no hay consulta, mostrar todos
    }
  }

  editFavorite(favorite: Favorite): void {
    this.selectedFavorite = { ...favorite };
    this.updatedName = favorite.favNombre;
  }

  saveFavorite(): void {
    if (this.selectedFavorite) {
      this.favoritesService.updateFavorite(this.selectedFavorite.idFavorite, this.updatedName).subscribe(
        (response) => {
          console.log('Favorite updated:', response);
          this.loadFavorites();
          this.selectedFavorite = null;
        },
        (error) => {
          console.error('Error updating favorite:', error);
        }
      );
    }
  }

  deleteFavorite(idFavorite: number): void {
    this.favoritesService.deleteFavorite(idFavorite).subscribe(
      (response) => {
        console.log('Favorite deleted:', response);
        this.loadFavorites();
      },
      (error) => {
        console.error('Error deleting favorite:', error);
      }
    );
  }
}

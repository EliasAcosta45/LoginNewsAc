import { Component, OnInit } from '@angular/core';
import { UserdbServiceService } from 'src/app/services/userdb-service.service'; // Asegúrate de que la ruta sea correcta

interface User {
  IdUser: number; // ID del usuario
  UserName: string; // Nombre del usuario
  Role: string; // Rol del usuario
}

@Component({
  selector: 'app-admin-crud',
  templateUrl: './admin-crud.component.html',
  styleUrls: ['./admin-crud.component.css']
})
export class AdminCrudComponent implements OnInit {
  users: User[] = [];
  filteredUsers: User[] = [];
  searchQuery: string = '';
  selectedUser: User | null = null;
  updatedName: string = '';
  updatedRole: string = '';

  constructor(private userService: UserdbServiceService) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.userService.getUsers().subscribe(
      (data: User[]) => {
        this.users = data;
        this.filteredUsers = data; // Inicialmente, la lista filtrada es igual a la original
      },
      (error) => {
        console.error('Error fetching users:', error);
      }
    );
  }

  searchUsers(): void {
    if (this.searchQuery.trim()) {
      this.userService.searchUser(this.searchQuery).subscribe(
        (data: User[]) => {
          this.filteredUsers = data;
        },
        (error) => {
          console.error('Error searching users:', error);
        }
      );
    } else {
      this.filteredUsers = this.users; // Si no hay consulta, mostrar todos
    }
  }

  editUser(user: User): void {
    this.selectedUser = { ...user };
    this.updatedName = user.UserName;
    this.updatedRole = user.Role;
  }

  saveUser(): void {
    if (this.selectedUser) {
      this.userService.updateUser(this.selectedUser.IdUser, this.updatedName, this.updatedRole).subscribe(
        (response) => {
          console.log('User updated:', response);
          this.loadUsers();
          this.selectedUser = null;
        },
        (error) => {
          console.error('Error updating user:', error);
        }
      );
    }
  }

  deleteUser(idUser: number): void {
    this.userService.deleteUser(idUser).subscribe(
      (response) => {
        console.log('User deleted:', response);
        this.loadUsers();
      },
      (error) => {
        console.error('Error deleting user:', error);
      }
    );
  }
}

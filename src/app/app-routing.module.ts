import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './auth/login/login.component';
import { RecoverPasswordComponent } from './auth/recover/recover.component';
import { AuthGuard } from './guards/auth.guard';
import { CommonTableComponent } from './shared/components/common-table/common-table.component'; // Importamos el componente
import { RegisterComponent } from './auth/register/register.component';
import { UserCrudComponent } from './user/components/user-crud/user-crud.component';

const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'admin', loadChildren: () => import('./admin/admin.module').then(m => m.AdminModule), canActivate: [AuthGuard] },
  { path: 'recover', component: RecoverPasswordComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'home', component: CommonTableComponent, canActivate: [AuthGuard] }, // Ahora home apunta al CommonTableComponent
  { path: 'favorites', component: UserCrudComponent, canActivate: [AuthGuard]  }, // UserModule maneja lo del usuario
  { path: '', redirectTo: '/home', pathMatch: 'full' }, // Página principal
  { path: '**', redirectTo: '/home' } 
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}

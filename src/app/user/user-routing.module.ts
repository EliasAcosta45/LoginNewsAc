import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../guards/auth.guard';
import { UserCrudComponent } from '../user/components/user-crud/user-crud.component';

const routes: Routes = [
  { path: 'favorites', component: UserCrudComponent, canActivate: [AuthGuard] } // Vista de favoritos
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserRoutingModule {}

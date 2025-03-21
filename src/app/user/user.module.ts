import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from 'src/app/shared/shared.module';
import { UserRoutingModule } from './user-routing.module';
import { UserCrudComponent } from './components/user-crud/user-crud.component';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [
    UserCrudComponent
  ],
  imports: [
    FormsModule,
    CommonModule,
    UserRoutingModule,
    RouterModule,
    [SharedModule]
  ]
})
export class UserModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from 'src/app/shared/shared.module';
import { AdminRoutingModule } from './admin-routing.module';
import { AdminCrudComponent } from './components/admin-crud/admin-crud.component';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

@NgModule({
  declarations: [
    AdminCrudComponent
  ],
  imports: [
    FormsModule,
    CommonModule,
    AdminRoutingModule,
    [RouterModule, AdminRoutingModule],
    [SharedModule],
    
  ]
})
export class AdminModule { }

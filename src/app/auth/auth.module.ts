import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';  // Asegúrate de importar RouterModule
import { RecoverService } from './recover-service.service';
import { RegisterComponent } from './register/register.component';
import { LoginComponent } from './login/login.component';
import { RecoverPasswordComponent } from './recover/recover.component';

@NgModule({
  declarations: [
    RegisterComponent,
    LoginComponent,
    RecoverPasswordComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    RouterModule  // Asegúrate de que esté aquí
  ],
  providers: [RecoverService]
})
export class AuthModule { }

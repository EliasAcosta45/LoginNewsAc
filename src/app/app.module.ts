import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { RouterModule } from '@angular/router';
import { AuthModule } from './auth/auth.module';  // Importa AuthModule aquí
import { AuthGuard } from './guards/auth.guard';
import { JwtInterceptorInterceptor } from './jwt-interceptor.interceptor';
import { NavbarComponent } from './navbar/navbar.component';
import { FormsModule } from '@angular/forms';
import { UserModule } from './user/user.module';


@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent
  ],
  imports: [
    UserModule, 
    FormsModule,
    BrowserModule,
    RouterModule,
    AppRoutingModule,
    HttpClientModule,
    CommonModule,
    AuthModule  // No es necesario FormsModule aquí
  ],
  providers: [AuthGuard,
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptorInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

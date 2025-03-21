import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit {
  isLoggedIn: boolean = false;
  currentYear: number = new Date().getFullYear();
  constructor(private router: Router) { }
  
  ngOnInit(): void {
    const token = localStorage.getItem('Token');
    this.isLoggedIn = token ? true : false;}

  logout() {{
    localStorage.removeItem('hasReloaded');
    localStorage.removeItem('Token');
    this.isLoggedIn = false;
    this.router.navigate(['/login']);
  }
}
}
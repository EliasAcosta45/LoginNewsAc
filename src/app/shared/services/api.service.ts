import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  private apiUrl = 'http://localhost/APIEliasAcosta/newsletter.php';

  constructor(private http: HttpClient) {}

  getNewsletters(): Observable<any> {
    return this.http.get(this.apiUrl); 
  }
}

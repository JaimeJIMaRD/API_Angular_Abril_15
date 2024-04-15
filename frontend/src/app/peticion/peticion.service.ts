import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
   
import {  Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
  
import { Peticion } from './peticion';
   
@Injectable({
  providedIn: 'root'
})
export class PeticionService {
   
  private apiURL = "http://127.0.0.1:8000/api";
   
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json'
    })
  }
  
  constructor(private httpClient: HttpClient) { }
   
  getAll(): Observable<Peticion[]> {
    return this.httpClient.get<Peticion[]>(this.apiURL + '/peticiones/')
    .pipe(
      catchError(this.errorHandler)
    )
  }

  getMine(): Observable<Peticion[]> {
    return this.httpClient.get<Peticion[]>(this.apiURL + '/mispeticiones/')
    .pipe(
      catchError(this.errorHandler)
    )
  }
   
  create(peticion:FormData): Observable<any> {
    const headers = new HttpHeaders();
    headers.append('Content-Type','multipart/form-data');
    headers.append('Accept','application/json');
    return this.httpClient.post<any>(this.apiURL + '/peticiones/', peticion, {headers:headers})
    .pipe(
      catchError(this.errorHandler)
    )
  }  
   
  find(id:Number): Observable<Peticion> {
    return this.httpClient.get<Peticion>(this.apiURL + '/peticiones/' + id)
    .pipe(
      catchError(this.errorHandler)
    )
  }
   
  update(id:Number, peticion:Peticion): Observable<Peticion> {
    return this.httpClient.put<Peticion>(this.apiURL + '/peticiones/' + id, JSON.stringify(peticion), this.httpOptions)
    .pipe(
      catchError(this.errorHandler)
    )
  }
   
  delete(id:Number){
    return this.httpClient.delete<Peticion>(this.apiURL + '/peticiones/' + id, this.httpOptions)
    .pipe(
      catchError(this.errorHandler)
    )
  }
    
  
  errorHandler(error: { error: { message: string; }; status: any; message: any; }) {
    let errorMessage = '';
    if(error.error instanceof ErrorEvent) {
      errorMessage = error.error.message;
    } else {
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(errorMessage);
 }

 firmar(id:number):Observable<any>{
    return this.httpClient.get(this.apiURL+'/peticiones/firmar'+id)
    .pipe(catchError(this.errorHandler))
 }

 getAllCategories(){
  
 }
}
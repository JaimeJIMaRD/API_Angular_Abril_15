
import { Component, OnInit } from '@angular/core';
import { PeticionService } from '../peticion.service';
import { Peticion } from '../peticion';
import { AuthStateService } from 'src/app/shared/auth-state.service';
import { Router } from '@angular/router';
import { TokenService } from 'src/app/shared/token.service';
import { AuthService } from 'src/app/shared/auth.service';
      
@Component({
  selector: 'app-myindex',
  templateUrl: './myindex.component.html',
  styleUrls: ['./myindex.component.css']
})
export class MyIndexComponent implements OnInit {
  isSignedIn!: boolean;    
  peticiones: Peticion[] = [];
  loggedUser: any;
    
  constructor(public peticionService: PeticionService,
    private auth: AuthStateService,
    private authService: AuthService,
    public router: Router,
    public token: TokenService) { }


  ngOnInit(): void {
    this.peticionService.getMine().subscribe((data: Peticion[])=>{
      this.peticiones = data;
      console.log(this.peticiones);
    })  
    this.auth.userAuthState.subscribe((val) => {
      this.isSignedIn = val;
    });
    this.getUserLogged();
  }
    

  deletePeticion(id:number){
    this.peticionService.delete(id).subscribe(res => {
         this.peticiones = this.peticiones.filter(item => item.id !== id);
         console.log('Petition deleted successfully!');
    })
  }
   
  getUserLogged(){
    this.authService.profileUser().subscribe((data)=>{
      this.loggedUser = data;
      console.log(this.loggedUser)
    })
    console.log(this.loggedUser)
  }
}

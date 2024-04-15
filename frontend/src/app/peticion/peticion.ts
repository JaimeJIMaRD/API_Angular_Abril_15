import { Data } from "@angular/router";

export interface Peticion {
    id: number;
    titulo: string;
    descripcion: string;
    destinatario: string;
    firmantes: number;
    estado: string;
    user_id: number;
    // categoria_id: number;
    user: any;
    categoria: any;
    file:string;
    files:any;
}
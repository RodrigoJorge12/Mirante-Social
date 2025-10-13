import { ServiceUsuario } from "@/Services/ServiceUsuario";

export class PresenterUsuario{
    service : ServiceUsuario;
    constructor(){
        this.service = new ServiceUsuario();
    }
    async validUserData(nome: string, login : string, senha : string){
        return true;
    }
    async createUser(nome: string, login : string, senha : string){
        const valid = await this.validUserData(nome, login, senha);
        if(!valid){
            return;
        }
        return await this.service.createUser(nome, login, senha);
    }
}

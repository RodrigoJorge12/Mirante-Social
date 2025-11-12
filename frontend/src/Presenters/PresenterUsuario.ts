import { ServiceUsuario } from "@/Services/ServiceUsuario";

export class PresenterUsuario{
    service : ServiceUsuario;
    constructor(){
        this.service = new ServiceUsuario();
    }
    async login(email : string, senha : string){
        return this.service.login(email, senha);
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
    async verifyEmailCode(email: string, code : string){
        return this.service.verifyEmailCode(email, code);
    }
    async verifyIfIsLogged(){
        return this.service.verifyIfIsLogged();
    }
}

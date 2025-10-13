export class ServiceUsuario{
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async createUser(nome: string, login : string, senha : string){
        try {
            const response = await fetch(this.backendURL + "/users", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json"
                },
                credentials: "include",
                body : JSON.stringify({
                    nome : nome,
                    login : login,
                    senha : senha
                })
            });
            if(!response.ok){
                return;
            }
            const valid = await response.json();
            if(valid){
                return valid;
            }
            return false;
        } catch (error) {
            console.error(error);
            return;
        }
    }
}
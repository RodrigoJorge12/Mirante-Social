export class ServiceUsuario{
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async createUser(nome: string, email : string, senha : string){
        try {
            const response = await fetch(this.backendURL + "/api/users", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json"
                },
                // credentials: "include",
                body : JSON.stringify({
                    nome : nome,
                    email : email,
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
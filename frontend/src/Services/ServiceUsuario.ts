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
                    name : nome,
                    email : email,
                    password : senha
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
    async verifyEmailCode(email: string, code : string){
                try {
            const response = await fetch(this.backendURL + "/api/verifyEmail", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json"
                },
                // credentials: "include",
                body : JSON.stringify({
                    email : email,
                    code : code
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
    async login(email : string, senha : string){
        try {
            const response = await fetch(this.backendURL + "/api/login", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json"
                },
                credentials: "include",
                body : JSON.stringify({
                    email : email,
                    password : senha
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
    async verifyIfIsLogged(){
        try {
            const response = await fetch(this.backendURL + "/api/verifyIfIsLogged", {
                method : "GET",
                headers : {
                    "Content-Type": "application/json"
                },
                credentials: "include",
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
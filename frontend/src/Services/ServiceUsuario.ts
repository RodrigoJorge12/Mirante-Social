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
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                },
                // credentials: "include",
                body : JSON.stringify({
                    name : nome,
                    email : email,
                    password : senha
                }),
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
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
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
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
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
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
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
    async logout(){
        try {
            const response = await fetch(this.backendURL + "/api/logout", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                },
                credentials: "include",
            });
            if(!response.ok){
                return;
            }
            const result = await response.json();
            return result;
        } catch (error) {
            console.error(error);
            return;
        }
    }
    async sendPasswordResetEmail(email: string){
        try {
            const response = await fetch(this.backendURL + "/api/sendPasswordResetEmail", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                },
                body : JSON.stringify({
                    email : email
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
    async resetPassword(email: string, code: string, password: string){  
        try {
            const response = await fetch(this.backendURL + "/api/resetPassword", {
                method : "POST",
                headers : {
                    "Content-Type": "application/json",
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                },
                body : JSON.stringify({
                    email : email,
                    code : code,
                    password : password
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
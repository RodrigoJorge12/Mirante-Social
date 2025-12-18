export class ServiceSocialProject {
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, wantsPersonalizedPage: boolean, selectedTemplate: string, latitude: number | null, longitude: number | null){
        try {
            const formData = new FormData()

            formData.append("name", name)
            formData.append("description", description)
            formData.append("address", address)
            formData.append("district", district)
            formData.append("city", city)
            formData.append("state", state)
            formData.append("zipCode", zipCode)
            formData.append("phone", phone)
            formData.append("websiteUrl", websiteUrl ?? "")
            formData.append("visualColor", visualColor)
            formData.append("activityArea", activityArea)
            formData.append("targetAudiences", JSON.stringify(targetAudiences))
            formData.append("wantsPersonalizedPage", wantsPersonalizedPage ? "true" : "false")
            formData.append("selectedTemplate", selectedTemplate)
            formData.append("latitude", latitude !== null ? latitude.toString() : "")
            formData.append("longitude", longitude !== null ? longitude.toString() : "")

            if (image) {
                formData.append("image", image)
            }
            const response = await fetch(this.backendURL + `/api/socialProject`, {
                method : "POST",
                credentials: "include",
                body : formData
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
    async GetAllProjects(){
        try {
            const response = await fetch(this.backendURL + `/api/socialProjects`);
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
    async GetProjectsByLoggedUser(){
        try {
            const response = await fetch(this.backendURL + `/api/socialProjectsByLoggedUser`, {
                method : "GET",
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
    async DeleteProject(id: number){
        try {
            const response = await fetch(this.backendURL + `/api/socialProject/${id}`, {
                method : "DELETE",
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
    async GetProjectById(id: number){
        try {
            const response = await fetch(this.backendURL + `/api/socialProject/${id}`, {
                method : "GET",
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
    async UpdateSocialProject( id: number, name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null){
        try {
            const formData = new FormData()

            formData.append("name", name)
            formData.append("description", description)
            formData.append("address", address)
            formData.append("district", district)
            formData.append("city", city)
            formData.append("state", state)
            formData.append("zipCode", zipCode)
            formData.append("phone", phone)
            formData.append("websiteUrl", websiteUrl ?? "")
            formData.append("visualColor", visualColor)
            formData.append("activityArea", activityArea)
            formData.append("targetAudiences", JSON.stringify(targetAudiences))

            if (image) {
                formData.append("image", image)
            }   
            formData.append("_method", "PUT");
            const response = await fetch(this.backendURL + `/api/socialProject/${id}`, {
                method : "POST",
                credentials: "include",
                body : formData
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
    async getProjectsNear(latitude: number, longitude: number, radius: number) {
        const url = new URL(`${this.backendURL}/api/projects/near`);

        url.searchParams.append("lat", latitude.toString());
        url.searchParams.append("lng", longitude.toString());
        url.searchParams.append("radius", radius.toString());

        const response = await fetch(url.toString(), {
        method: "GET",
        credentials: "include", // importante se você usa auth por cookie
        headers: {
            "Accept": "application/json",
        },
        });

        if (!response.ok) {
        throw new Error("Erro ao buscar projetos próximos");
        }

        return await response.json();
    }
}
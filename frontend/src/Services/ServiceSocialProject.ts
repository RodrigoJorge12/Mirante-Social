export class ServiceSocialProject {
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null){
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
}
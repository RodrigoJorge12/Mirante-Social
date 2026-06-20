export class ServiceSocialProject {
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, wantsPersonalizedPage: boolean, selectedTemplate: string, lat?: number | null, lng?: number | null, instagramUrl?: string, facebookUrl?: string, operatingHours?: string, mission?: string, galleryFiles?: File[]){
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
            if (lat != null) formData.append("lat", String(lat))
            if (lng != null) formData.append("lng", String(lng))
            formData.append("instagramUrl", instagramUrl ?? "")
            formData.append("facebookUrl", facebookUrl ?? "")
            formData.append("operatingHours", operatingHours ?? "")
            formData.append("mission", mission ?? "")

            if (image) {
                formData.append("image", image)
            }
            if (galleryFiles && galleryFiles.length) {
                galleryFiles.forEach(f => formData.append("gallery[]", f))
            }
            const response = await fetch(this.backendURL + `/api/socialProject`, {
                method : "POST",
                credentials: "include",
                body : formData,
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                }
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
            const response = await fetch(this.backendURL + `/api/socialProjects`, {    
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                },
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
    async GetProjectsByLoggedUser(){
        try {
            const response = await fetch(this.backendURL + `/api/socialProjectsByLoggedUser`, {
                method : "GET",
                credentials: "include",
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                }
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
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                }
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
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                }
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
    async UpdateSocialProject( id: number, name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, instagramUrl?: string, facebookUrl?: string, operatingHours?: string, mission?: string){
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
            formData.append("instagramUrl", instagramUrl ?? "")
            formData.append("facebookUrl", facebookUrl ?? "")
            formData.append("operatingHours", operatingHours ?? "")
            formData.append("mission", mission ?? "")

            if (image) {
                formData.append("image", image)
            }
            formData.append("_method", "PUT");
            const response = await fetch(this.backendURL + `/api/socialProject/${id}`, {
                method : "POST",
                credentials: "include",
                body : formData,
                headers: {
                    "X-App-Key": import.meta.env.VITE_APP_TOKEN
                }
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
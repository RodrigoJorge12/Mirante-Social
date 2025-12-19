import { ServiceSocialProject } from "../Services/ServiceSocialProject";

export class PresenterSocialProject {
  private service = new ServiceSocialProject();
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, wantsPersonalizedPage: boolean, selectedTemplate: string, latitude: number | null, longitude: number | null){      
        return await this.service.CreateSocialProject( name, description, address, district, city, state, zipCode, phone, websiteUrl, visualColor, activityArea, targetAudiences, image, wantsPersonalizedPage, selectedTemplate, latitude, longitude);
    }
    async GetAllProjects(){
        return await this.service.GetAllProjects();
    }
    async GetProjectsByLoggedUser(){
        return await this.service.GetProjectsByLoggedUser();
    }
    async DeleteProject(id: number){
        return await this.service.DeleteProject(id);
    }
    async GetProjectById(id: number){
        return await this.service.GetProjectById(id);
    }
    async UpdateSocialProject( id: number, name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, contactEmail: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null){
        return await this.service.UpdateSocialProject( id, name, description, address, district, city, state, zipCode, phone, contactEmail, websiteUrl, visualColor, activityArea, targetAudiences, image);
    }
    async getProjectsNear(latitude: number, longitude: number, radius: number) {
        return await this.service.getProjectsNear(latitude, longitude, radius);
    }
}
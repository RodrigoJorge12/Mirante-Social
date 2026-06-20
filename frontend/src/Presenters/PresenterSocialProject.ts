import { ServiceSocialProject } from "../Services/ServiceSocialProject";

export class PresenterSocialProject {
  private service = new ServiceSocialProject();
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, wantsPersonalizedPage: boolean, selectedTemplate: string, lat?: number | null, lng?: number | null, instagramUrl?: string, facebookUrl?: string, operatingHours?: string, mission?: string, galleryFiles?: File[]) {
        return await this.service.CreateSocialProject( name, description, address, district, city, state, zipCode, phone, websiteUrl, visualColor, activityArea, targetAudiences, image, wantsPersonalizedPage, selectedTemplate, lat, lng, instagramUrl, facebookUrl, operatingHours, mission, galleryFiles);
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
    async UpdateSocialProject( id: number, name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null, instagramUrl?: string, facebookUrl?: string, operatingHours?: string, mission?: string){
        return await this.service.UpdateSocialProject( id, name, description, address, district, city, state, zipCode, phone, websiteUrl, visualColor, activityArea, targetAudiences, image, instagramUrl, facebookUrl, operatingHours, mission);
    }
}
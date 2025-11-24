import { ServiceSocialProject } from "../Services/ServiceSocialProject";

export class PresenterSocialProject {
  private service = new ServiceSocialProject();
    async CreateSocialProject( name: string, description: string, address: string, district: string, city: string, state: string, zipCode: string, phone: string, websiteUrl: string, visualColor: string, activityArea: string, targetAudiences: string[], image: File | null){        
        return await this.service.CreateSocialProject( name, description, address, district, city, state, zipCode, phone, websiteUrl, visualColor, activityArea, targetAudiences, image);
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
}
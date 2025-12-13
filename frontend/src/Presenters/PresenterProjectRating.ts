import { ServiceProjectRating } from "../Services/ServiceProjectRating";

export class PresenterProjectRating {
  private service = new ServiceProjectRating();
  async postRating(projectId: number, rating: number, feedback_text?: string){
    return await this.service.postRating(projectId, rating, feedback_text);
  }
  async getSummary(projectId: number){
    return await this.service.getSummary(projectId);
  }
  async getMine(projectId: number){
    return await this.service.getMine(projectId);
  }
  async list(projectId: number, page = 1, size = 10){
    return await this.service.list(projectId, page, size);
  }
}

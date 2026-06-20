import { ServicePersonalizedPage } from "../Services/ServicePersonalizedPage";

export class PresenterPersonalizedPage {
  private service = new ServicePersonalizedPage();

  async getPageData(slug: string) {
    return await this.service.getPageData(slug);
  }
  async getUrlByProjectId(projectId: number) {
    const slug = await this.service.getSlugByProjectId(projectId);
    const baseUrl = window.location.origin;
    const URL = baseUrl + `/personalizedPages/${slug}`;
    return URL
  }
  async updateGallery(pageId: number, files: File[]) {
    return await this.service.updateGallery(pageId, files);
  }
}

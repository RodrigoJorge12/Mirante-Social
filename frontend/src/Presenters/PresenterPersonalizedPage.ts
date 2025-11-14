import { ServicePersonalizedPage } from "../Services/ServicePersonalizedPage";

export class PresenterPersonalizedPage {
  private service = new ServicePersonalizedPage();

  async getPageData(slug: string) {
    return await this.service.getPageData(slug);
  }
}

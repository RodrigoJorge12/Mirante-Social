import { ServiceReport } from "../Services/ServiceReport";

export class PresenterReport {
  private service = new ServiceReport();

  async createReport(socialProjectId: number, category: string, reason: string) {
    return await this.service.createReport(socialProjectId, category, reason);
  }
  async getMyReports() {
    return await this.service.getMyReports();
  }
}


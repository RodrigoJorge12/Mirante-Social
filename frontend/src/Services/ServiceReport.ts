export class ServiceReport {
  backendURL: string;
  constructor() {
    this.backendURL = import.meta.env.VITE_API_URL;
  }
  async createReport(socialProjectId: number, category: string, reason: string) {
    try {
      const response = await fetch(this.backendURL + `/api/reports`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ social_project_id: socialProjectId, category, reason }),
      });
      if (!response.ok) return false;
      const json = await response.json();
      return json?.success ? json.data : false;
    } catch (e) {
      console.error(e);
      return false;
    }
  }
  async getMyReports() {
    try {
      const response = await fetch(this.backendURL + `/api/reports/mine`, {
        method: "GET",
        credentials: "include",
      });
      if (!response.ok) return false;
      const json = await response.json();
      return json?.success ? json.data : false;
    } catch (e) {
      console.error(e);
      return false;
    }
  }
}


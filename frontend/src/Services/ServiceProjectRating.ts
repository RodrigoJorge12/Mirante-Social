export class ServiceProjectRating {
  backendURL: string;
  constructor(){
    this.backendURL = import.meta.env.VITE_API_URL;
  }
  async postRating(projectId: number, rating: number, feedback_text?: string){
    const response = await fetch(this.backendURL + `/api/projects/${projectId}/ratings`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ rating, feedback_text })
    });
    if(!response.ok) return false;
    return await response.json();
  }
  async getSummary(projectId: number){
    const r = await fetch(this.backendURL + `/api/projects/${projectId}/ratings/summary`, { credentials: "include" });
    if(!r.ok) return false;
    return await r.json();
  }
  async getMine(projectId: number){
    const r = await fetch(this.backendURL + `/api/projects/${projectId}/ratings/mine`, { credentials: "include" });
    if(!r.ok) return false;
    return await r.json();
  }
  async list(projectId: number, page = 1, size = 10){
    const r = await fetch(this.backendURL + `/api/projects/${projectId}/ratings?page=${page}&size=${size}`, { credentials: "include" });
    if(!r.ok) return false;
    return await r.json();
  }
}

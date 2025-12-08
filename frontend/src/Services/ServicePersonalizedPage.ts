export class ServicePersonalizedPage {
    backendURL : string;
    constructor(){
        this.backendURL = import.meta.env.VITE_API_URL;
    }
    async getPageData( slug: string){
        try {
            const response = await fetch(this.backendURL + `/api/personalized-page/${slug}`, {
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
    async getSlugByProjectId(projectId: number){
        try {
            const response = await fetch(this.backendURL + `/api/personalized-page/slug-by-project/${projectId}`, {
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
                return valid.data.slug;
            }
            return false;
        } catch (error) {
            console.error(error);
            return;
        }
    }
}
// dashboar.model.ts
export interface DashboardData {
  cards: {
    title: string;
    value: string;
    trend: string;
    icon: string;
  }[];
  tasks: {
    title: string;
    description: string;
    date: string;
    color: string;
    icon: string;
  }[];
  chartData: {
    labels: string[];
    datasets: { label: string; data: number[] }[];
  };
}
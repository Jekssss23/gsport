import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type LucideIcon } from 'lucide-react';

interface KPICardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  change?: {
    value: number;
    isPositive: boolean;
  };
  icon: LucideIcon;
  trend?: 'up' | 'down' | 'stable';
}

export function KPICard({
  title,
  value,
  subtitle,
  change,
  icon: Icon,
  trend,
}: KPICardProps) {
  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
        <Icon className="h-4 w-4 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        {subtitle && <p className="text-xs text-muted-foreground mt-1">{subtitle}</p>}
        {change && (
          <div
            className={`text-xs mt-2 ${
              change.isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
            }`}
          >
            {change.isPositive ? '↑' : '↓'} {Math.abs(change.value)}%{' '}
            {change.isPositive ? 'increase' : 'decrease'}
          </div>
        )}
      </CardContent>
    </Card>
  );
}

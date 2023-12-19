import { Skeleton } from '@/components/ui/skeleton'

export default function PhotosSkeleton() {
  return (
    <div className="grid grid-cols-6 gap-4 px-8 pt-4">
      {Array.from({ length: 6*6 }).map((_, i) => (
        <Skeleton key={i} className="h-64" />
      ))}
    </div>
  )
}

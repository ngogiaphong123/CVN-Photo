import { useParams } from 'react-router'
import PhotosSkeleton from '@components/layouts/photos-skeleton'
import { motion } from 'framer-motion'
import { Skeleton } from '@components/ui/skeleton'
import { useCategory, useCategoryPhotos } from '@/hooks/category.hook'
import PhotoImage from '@/components/photo-image'

export default function CategoryDetail() {
  const { categoryId } = useParams()
  const { data: category } = useCategory(categoryId)
  const { data: photos, isLoading, isError } = useCategoryPhotos(categoryId)

  const renderPage = () => {
    if (isError) return <div>error</div>
    if (isLoading && !photos) {
      return (
        <div className="flex flex-col h-screen pt-20">
          <div className="flex flex-col justify-center gap-4 p-4">
            <Skeleton className="h-10 w-100" />
            <Skeleton className="h-10 w-100" />
            <PhotosSkeleton />
          </div>
        </div>
      )
    }
    if (!photos || !category) return <div>no data</div>
    return (
      <div className="flex flex-col h-screen pt-20">
        <div className="flex flex-col justify-center gap-4 p-4">
          <div className="flex text-xl font-bold uppercase md:text-2xl text-accent">
            {category.name} - {category.numPhotos} photos
          </div>
          <div className="font-semi text-muted-foreground">
            Create at {new Date(category.createdAt).toDateString()}
          </div>
          <div className="flex flex-wrap justify-between gap-4">
            {photos.map(photo => (
              <PhotoImage photo={photo} />
            ))}
          </div>
        </div>
      </div>
    )
  }
  return (
    <motion.div
      key={categoryId}
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
    >
      {renderPage()}
    </motion.div>
  )
}

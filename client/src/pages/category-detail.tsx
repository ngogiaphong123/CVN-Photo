import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { useParams } from 'react-router'
import { getPhotosInCategory } from '@redux/slices/photo.slice'
import { AppDispatch, useAppSelector } from '@redux/store'
import { getCategory } from '@redux/slices/category.slice'
import PhotosSkeleton from '../components/layouts/photos-skeleton'
import { motion } from 'framer-motion'
import { Skeleton } from '../components/ui/skeleton'

export default function CategoryDetail() {
  const { categoryId } = useParams()
  const dispatch = useDispatch<AppDispatch>()
  const photos = useAppSelector(state => state.photo).photos
  const category = useAppSelector(state => state.category).category
  const loading = useAppSelector(state => state.photo).loading

  useEffect(() => {
    dispatch(getCategory(categoryId))
    dispatch(getPhotosInCategory(categoryId))
  }, [categoryId])

  const renderPage = () => {
    if (loading) {
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
    return (
      <div className="flex flex-col h-screen pt-20">
        <div className="flex flex-col justify-center gap-4 p-4">
          <div className="flex text-xl font-bold uppercase md:text-2xl text-accent">
            {category.name} - {category.numPhotos} photos
          </div>
          <div className="font-semi text-muted-foreground">
            Create at {new Date(category.createdAt).toDateString()}
          </div>
          <div className="flex flex-wrap items-center justify-start gap-4">
            {photos.map(photo => (
              <div className="" key={photo.id}>
                <img
                  loading="lazy"
                  className="object-cover h-24 transition-all duration-200 ease-in-out shadow-lg hover:scale-105 md:h-48"
                  src={photo.url}
                  alt={photo.name}
                />
              </div>
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

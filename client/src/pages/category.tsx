import { Button } from '@components/ui/button'
import { Separator } from '@components/ui/separator'
import { useAppSelector } from '@redux/store'
import { motion } from 'framer-motion'
import { Link } from 'react-router-dom'

export default function Category() {
  const categories = useAppSelector(state => state.category).categories.filter(
    category => {
      return category.name !== 'uncategorized'
    },
  )
  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="pt-20"
    >
      <div className="flex items-center justify-between px-8 pb-4">
        <p className="text-xl text-primary">Category</p>
        <div>
          <Button className="text-accent" variant={'ghost'} size="lg">
            Create category
          </Button>
        </div>
      </div>
      <Separator />
      <div className="grid grid-cols-3 gap-2 p-4 md:grid-cols-4 lg:grid-cols-6">
        {categories.map(category => (
          <Link key={category.id} to={`/category/${category.id}`} className="">
            <div
              key={category.id}
              className="flex flex-col items-center justify-center gap-2 h-72"
            >
              <img
                src={category.url}
                alt={category.name}
                className="h-64 transition-all duration-200 ease-in-out rounded-lg hover:rounded-none"
              />
              <div className="text-black">{category.name}</div>
              <div className="text-black">{category.numPhotos}</div>
            </div>
          </Link>
        ))}
      </div>
    </motion.div>
  )
}

export default function Loading() {
  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="flex flex-col items-center">
        <div className="w-8 h-8 border-b rounded-full bg-primary animate-bounce"></div>
        Wait a moment...
      </div>
    </div>
  )
}
